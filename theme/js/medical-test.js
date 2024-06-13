document.addEventListener('DOMContentLoaded', function () {
    // Get the patientID from localStorage or wherever it's stored
    const patientData = JSON.parse(localStorage.getItem('patientData'));
    const patientID = patientData.patientID;
    console.log("Patient ID: ", patientID);

    const medicalTestsList = document.getElementById('medicaltest-list');

    async function fetchMedicalTests(patientID) {
        try {
            const response = await fetch('php/get-medicaltest.php?patientID=' + patientID);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const medicalTests = await response.json();

            if (Array.isArray(medicalTests) && medicalTests.length) {
                medicalTests.forEach(medicalTest => {
                    const medicalTestItem = document.createElement('div');
                    medicalTestItem.classList.add('medical-test-item');

                    const medicalTestBox = document.createElement('div');
                    medicalTestBox.classList.add('medical-test-box');

                    medicalTestBox.innerHTML = `
                        <h2>Date: ${medicalTest.date}</h2>
                        <p>Result: ${medicalTest.result}</p>
                    `;

                    medicalTestItem.appendChild(medicalTestBox);
                    medicalTestsList.appendChild(medicalTestItem);
                });
            } else {
                const noMedicalTestsMessage = document.createElement('p');
                noMedicalTestsMessage.textContent = 'No medical tests found';
                medicalTestsList.appendChild(noMedicalTestsMessage);
            }
        } catch (error) {
            console.error('Error fetching medical tests:', error);
            const errorMessage = document.createElement('p');
            errorMessage.textContent = 'Error fetching medical tests';
            medicalTestsList.appendChild(errorMessage);
        }
    }
    fetchMedicalTests(patientID);
});