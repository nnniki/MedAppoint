function reserveAppointment(event, logged, appointmentId, patientUsername) {
  
      try {

        if (logged == 0) {
            document.getElementById(`reserve-appointment-container-${appointmentId}`).innerHTML = "Невалидна резервация. Моля, влезте в профила си!";
            return;
        }
        const response = fetch('reserve_appointment.php', {
            method: 'POST',
            body: new URLSearchParams({
                'appointment_id': appointmentId,
                'patient_username': patientUsername,
            })
        
        })
        .then(reqResponse => reqResponse.text())
        .then(text => {  
            document.getElementById(`reserve-appointment-container-${appointmentId}`).innerHTML = text;
        })
       
    } catch (error) {
        console.error('Error:', error);
    }
}
