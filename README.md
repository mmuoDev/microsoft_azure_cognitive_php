# microsoft_azure_cognitive_php
A PHP Wrapper for Microsoft Azure Cognitive Services 

1. Run Composer install to install packages
2. Copy .env.example to .env and set the appropriate values. 
  - For speaker verification, you can get the SPEECH_KEY and SPEECH BASE_URL from your Microsoft Azure acccount.
  
## Speaker Verification
1. Install FFMPEG To allow you convert audio files to wav format. For ubuntu, sudo apt install ffmpeg. See https://ffmpeg.org/
2. Initiate a Speech object 
- $speaker = new Speech()
3. Create  Verification Profile
- $speaker->createVerificationProfile()
4. Enrol a Verification Profile
- $speaker->enrolVerificationProfile($audio, $verification_id)
- $audio is the audio file in wav format and $verification_id is the id of the speaker
- To convert to wav format, $speaker->convertAudio($audio)
5. Verify Speaker
- $speaker->verifySpeaker($audio, $verification_id)
- $audio is the audio file in wav format and $verification_id is the id of the speaker
- To convert to wav format, $speaker->convertAudio($audio)

##Details Microsoft Azure Speaker Recognition can be found here: https://azure.microsoft.com/en-us/services/cognitive-services/speaker-recognition/
