function addNote(event, appointmentId) {
    event.preventDefault();
    const noteInput = document.getElementById('note-input-' + appointmentId);
    const note = noteInput.value;

    try {
        const response = fetch('add_note.php', {
            method: 'POST',
            body: new URLSearchParams({
                'appointment_id': appointmentId,
                'note': note
            })
        
        })
        .then(noteResponse => noteResponse.text())
        .then(text => {
            document.getElementById(`note-container-${appointmentId}`).innerHTML = `${text}`;
        })
       
    } catch (error) {
        console.error('Error:', error);
    }
}