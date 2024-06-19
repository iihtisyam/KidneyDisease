document.addEventListener('DOMContentLoaded', function () {
    // Get the patientID from localStorage or wherever it's stored
    const patientData = JSON.parse(localStorage.getItem('patientData'));
    const patientID = patientData.patientID;
    console.log("Patient ID: ", patientID);

    async function fetchAppointments(patientID) {
        try {
            const response = await fetch('php/get-appointment.php?patientID=' + patientID);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const { upcomingAppointments, attendedAppointments } = await response.json();

            const upcomingList = document.getElementById('upcoming-appointments-list');
            const attendedList = document.getElementById('attended-appointments-list');

            // Ensure the elements exist
            if (!upcomingList) {
                throw new Error('Element with ID upcoming-appointments-list not found');
            }
            if (!attendedList) {
                throw new Error('Element with ID attended-appointments-list not found');
            }

            // Render upcoming appointments
            if (Array.isArray(upcomingAppointments) && upcomingAppointments.length) {
                upcomingAppointments.forEach(appointment => {
                    const appointmentItem = document.createElement('div');
                    appointmentItem.classList.add('appointment-item');

                    const appointmentBox = document.createElement('div');
                    appointmentBox.classList.add('appointment-box');

                    appointmentBox.innerHTML = `
                        <h2>Date: ${appointment.date}</h2>
                        <p>Time: ${appointment.startTime} - ${appointment.endTime}</p>
                        <p>Type: ${appointment.details}</p>
                        <p><strong>Status: To be attended <strong><span class="icon">📅</span></p>
                    `;

                    appointmentItem.appendChild(appointmentBox);
                    upcomingList.appendChild(appointmentItem);
                });
            } else {
                const noAppointmentsMessage = document.createElement('p');
                noAppointmentsMessage.textContent = 'No upcoming appointments found';
                upcomingList.appendChild(noAppointmentsMessage);
            }

            // Render attended appointments
            if (Array.isArray(attendedAppointments) && attendedAppointments.length) {
                attendedAppointments.forEach(appointment => {
                    const appointmentItem = document.createElement('div');
                    appointmentItem.classList.add('appointment-item');

                    const appointmentBox = document.createElement('div');
                    appointmentBox.classList.add('appointment-box');

                    appointmentBox.innerHTML = `
                        <h2>Date: ${appointment.date}</h2>
                        <p>Time: ${appointment.startTime} - ${appointment.endTime}</p>
                        <p>Type: ${appointment.details}</p>
                        <p><strong>Status: Complete</strong> <span class="icon">✔️</span></p>
                    `;

                    appointmentItem.appendChild(appointmentBox);
                    attendedList.appendChild(appointmentItem);
                });
            } else {
                const noAppointmentsMessage = document.createElement('p');
                noAppointmentsMessage.textContent = 'No attended appointments found';
                attendedList.appendChild(noAppointmentsMessage);
            }
        } catch (error) {
            console.error('Error fetching appointments:', error);
            const errorMessage = document.createElement('p');
            errorMessage.textContent = 'Error fetching appointments';
            const upcomingList = document.getElementById('upcoming-appointments-list');
            const attendedList = document.getElementById('attended-appointments-list');

            if (upcomingList) {
                upcomingList.appendChild(errorMessage);
            }
            if (attendedList) {
                attendedList.appendChild(errorMessage);
            }
        }
    }
    fetchAppointments(patientID);
});
