document.addEventListener('DOMContentLoaded', function () {
    // Get the patientID from localStorage or wherever it's stored
    const patientData = JSON.parse(localStorage.getItem('patientData'));
    const patientID = patientData.patientID;
    console.log("Patient ID: ",patientID);

    async function fetchAppointments(patientID) {
        try {
            const response = await fetch('php/get-appointment.php?patientID=' + patientID);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const appointments = await response.json();
    
            const appointmentsList = document.getElementById('appointments-list');
    
            if (Array.isArray(appointments) && appointments.length) {
                appointments.forEach(appointment => {
                    const appointmentItem = document.createElement('div');
                    appointmentItem.classList.add('appointment-item');
                    
                    const appointmentBox = document.createElement('div');
                    appointmentBox.classList.add('appointment-box');
                    
                    appointmentBox.innerHTML = `
                        <h2>Date: ${appointment.date}</h2>
                        <p>Time: ${appointment.startTime} - ${appointment.endTime}</p>
                        <p>Type: ${appointment.details}</p>
                    `;
                    
                    appointmentItem.appendChild(appointmentBox);
                    appointmentsList.appendChild(appointmentItem);
                });
            } else {
                const noAppointmentsMessage = document.createElement('p');
                noAppointmentsMessage.textContent = 'No upcoming appointments found';
                appointmentsList.appendChild(noAppointmentsMessage);
            }
        } catch (error) {
            console.error('Error fetching appointments:', error);
            const appointmentsList = document.getElementById('appointments-list');
            const errorMessage = document.createElement('p');
            errorMessage.textContent = 'Error fetching appointments';
            appointmentsList.appendChild(errorMessage);
        }
    }
    fetchAppointments(patientID);
});