# Appointment Scheduler External Module
 

### What does it do?
This custom EM will allow Admins to create/import office hours into REDCap project and allow users to book these appointments. Public users can reserve these hours for from Calendar page. Or Users can be prompt to scheduler a follow up call/meeting after completing a survey.  

User can view/manage her/his reservations. 

Admins can view/manage/reschedule her/his or other calendars. 


### REDCap Project



#### Arms:
You need two main arms in Appointment Scheduler Project.

1. Slots Arm. This arm will contain Time Slots events and instruments. 
2. Reservation Arm. this will contain Reservation events and instruments. Also It will include survey instrument if applicable. 


####Main Instruments:

1. Time Slots Instrument [ZIP](zip/Slots_Instrument.zip):
    1. Start Time (start)
    2. End Time (end)
    3. Instructor (instructor)
    4. Location (location)
    5. Notes (notes)
    6. Log (log)
    7. Number of Participants (number_of_participants)
    8. Slot status (slot_status)
2. Reservation Instrument [ZIP](zip/Reservation_Instrument.zip):
    1. Slot ID (slot_if)
    2. SUNetID(sunet_id Optional)
    3. Name (name)
    4. Email (email)
    5. Mobile (mobile)
    6. Notes (participant_notes)
    7. Private (private)
    8. Status (participant_status)
    9. Location (location)
    10. Department(department optional)
    11. Project ID (project_id). 
    
####Optional Instruments:
If you are using Appointment Scheduler to follow up with survey participants. You need to create a field in your survey that will be replaced with reservation button and will hold the Slot ID value after survey is submitted. And select this field in config.json.   

####EM configuration:
1.  For each Appointment type you need to create new REDCap instance (E.g instance for office hour and another for training).
.Below are configuration options for each Instance. 

    1. You need to define description of the Appointment Scheduler. 
    2. Select Slots event name which MUST include the slots instrument.
    3. Select Reservation event which MUST include the reservation instrument. 
    4. Optional: select survey instrument that will allow the participant to schedule a follow up call/meeting after completing the survey.   
    5. If you select a survey instrument for the scheduler you need to select a field that will be replaced with Scheduler Appointment button then will hold the reservation record ID after saving the Appointment. 
    6. Option to integrate REDCap calendar into the calendar view of the Appointment Scheduler. 
    7. Text description for Notes field. 
    8. Option to display Notes textarea. 
    9. Option to display a list of project the User is part of. This option will allow user to tie their appointment to a project. 
    10. Type an email that the EM to send reservation communication from. 
    11. Type sender name. 
    12. Type Calendar Email subject.
    13. Type Calendar Email body.
    
2. If you want to send mobile text messages for reservation confirmation you can define Twilio configuration(configured only once for all instances).
    1. Phone number country code(just numbers without leading zeros).
    2. Phone number. 
    3. Twilio SID.
    4. Twilio token. 



####URL: 
http://[HOST_NAME]/[REDCap_EM_FOLDER]/?prefix=appointment_scheduler&page=src%2Ftypes&projectid=[PROJECT_ID]

####Sample Projects: 
1. Regular Appointment Scheduler [XML](xml/Sample_Appointment_Scheuler.xml). 
2. Survey Follow up scheduler [XML](xml/Survey_Followup_Scheduler.xml)..