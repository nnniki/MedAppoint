async function addNote(event, appointmentId) {
    event.preventDefault();
    const noteInput = document.getElementById('note-input-' + appointmentId);
    const note = noteInput.value;

    try {
        const response = await fetch('add_note.php', {
            method: 'POST',
            body: new URLSearchParams({
                'appointment_id': appointmentId,
                'note': note
            })
        });

        if (response.ok) {
            const updatedNote = await response.text();
            document.getElementById('notes-' + appointmentId).innerText = updatedNote;
            location.reload();
        } else {
            console.error('Failed to add note');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}