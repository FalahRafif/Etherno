document.addEventListener('DOMContentLoaded', function() {
    const dateRangeSelect = document.getElementById('date_range');
    const dateStartInput = document.getElementById('date_start');
    const dateEndInput = document.getElementById('date_end');

    function toggleDateInputs() {
        const selectedValue = dateRangeSelect.value;

        if (selectedValue === 'custom') {
            dateStartInput.disabled = false;
            dateEndInput.disabled = false;
            dateStartInput.closest('.col').style.display = 'block';
            dateEndInput.closest('.col').style.display = 'block';
        } else {
            dateStartInput.disabled = true;
            dateEndInput.disabled = true;
            dateStartInput.closest('.col').style.display = 'none';
            dateEndInput.closest('.col').style.display = 'none';
        }
    }

    dateRangeSelect.addEventListener('change', toggleDateInputs);

    toggleDateInputs();
});