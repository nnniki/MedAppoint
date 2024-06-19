function cancelAppointment(event, appointmentId, type) {
    try {

      const response = fetch('../../business/cancel_appointment.php', {
          method: 'POST',
          body: new URLSearchParams({
              'appointment_id': appointmentId,
              'type': type,
          })
      
      })
      .then(reqResponse => reqResponse.text())
      .then(text => {  
          document.getElementById(`cancel-appointment-container-${appointmentId}`).innerHTML = text;
      })
     
  } catch (error) {
      console.error('Error:', error);
  }
}
