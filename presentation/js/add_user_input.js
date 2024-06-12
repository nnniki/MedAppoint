function addUserInput(event, appointmentId, type) {
    event.preventDefault();
    const userInput = document.getElementById(`${type}-input-` + appointmentId);
    const input = userInput.value;

    try {
        const response = fetch('../../business/add_user_input.php', {
            method: 'POST',
            body: new URLSearchParams({
                'appointment_id': appointmentId,
                'input': input,
                'type': type,
            })

        })
            .then(reqResponse => reqResponse.text())
            .then(text => {
                if (type === "rating") {
                    const number = parseInt(text, 10);
                    const maxStars = 5;
                    let stars = '';

                    for (let i = 0; i < number; i++) {
                        stars += '★';
                    }

                    for (let i = number; i < maxStars; i++) {
                        stars += '☆';
                    }
                    document.getElementById(`${type}-container-${appointmentId}`).innerHTML = stars;
                } else {
                    document.getElementById(`${type}-container-${appointmentId}`).innerHTML = text;
                }
            })

    } catch (error) {
        console.error('Error:', error);
    }
}