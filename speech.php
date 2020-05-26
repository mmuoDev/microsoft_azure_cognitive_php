<?php
//Speaker recognition using Microsoft Azure

use Exception;
use GuzzleHttp\Client;
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createMutable(__DIR__);
$dotenv->load();

class Speech {

    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    //create a verification profile
    public function createVerificationProfile(){
        $data = [
            'locale' => $_ENV['SPEECH_LOCALE']
        ];
        // dd($data);
        $jsData = json_encode($data);
        $headers =  [
            'content-type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $_ENV['SPEECH_KEY']
        ];
        try{
            $call = $this->client->post(
                $_ENV['SPEECH_BASE_URL'].'verificationProfiles',
                [
                    "headers" => $headers,
                    "body" => $jsData
                ]
            );
            $response = json_decode($call->getBody()->getContents(), true);
            if(isset($response['verificationProfileId'])){
                return $response['verificationProfileId'];
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
    }

    //convert audio to wav file - sudo apt install ffmpeg
    public function convertAudio($audio){
        try{
            $file = time().'.wav';
            $output = './audio/'.$file; //output folder
            exec("ffmpeg -i {$audio} -ab 16  -ar 16000 {$output} -y");
            return $file;
        }catch(Exception $ex){
            return false;
        }
    }

    //Enrol verification profile
    public function enrolVerificationProfile($audio, $verification_id){
        $data = [
            'locale' => $_ENV['SPEECH_LOCALE']
        ];
        $headers =  [
            'content-type' => 'multipart/form-data',
            'Ocp-Apim-Subscription-Key' => $_ENV['SPEECH_KEY']
        ];
       
        try{
            $call = $this->client->post(
                $_ENV['SPEECH_BASE_URL'].'verificationProfiles/'.$verification_id.'/enroll',
                [
                    "headers" => $headers,
                    "body" => file_get_contents('./audio/'.$audio)
                ]
            );
            $response = json_decode($call->getBody()->getContents(), true);
            //dd($response);
            if(isset($response['enrollmentStatus'])){
                if(strtolower($response['enrollmentStatus']) == "enrolled"){
                    return true;
                }elseif(strtolower($response['enrollmentStatus']) == "enrolling"){
                    return $response['remainingEnrollments'];
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
    }

    //verify a user
    public function verifySpeaker($audio, $verification_id){
        $headers =  [
            'content-type' => 'multipart/form-data',
            'Ocp-Apim-Subscription-Key' => $_ENV['SPEECH_KEY']
        ];
       
        try{
            $call = $this->client->post(
                $_ENV['SPEECH_BASE_URL'].'verify?verificationProfileId='.$verification_id,
                [
                    "headers" => $headers,
                    "body" => file_get_contents('./audio/'.$audio)
                ]
            );
            $response = json_decode($call->getBody()->getContents(), true);
            if(isset($response['result'])){
                return $response['result']; //default result - Accept
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
    }

    //get training phrases
    public function getTrainingPhrases(){
        $data = [
            'locale' => $_ENV['SPEECH_LOCALE']
        ];
        // dd($data);
        $jsData = json_encode($data);
        $headers =  [
            'content-type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $_ENV['SPEECH_KEY']
        ];
        try{
          
            $call = $this->client->get(
                $_ENV['SPEECH_BASE_URL'].'verificationPhrases?locale='.$_ENV['SPEECH_LOCALE'],
                [
                    "headers" => $headers
                ]
            );
            $response = json_decode($call->getBody()->getContents(), true);
            if(isset($response)){
                return $response; //my password is not your business
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
    }
}