<?php
require '../user_module/database.php';

$formTitle = "Add New Recipe";
$formAction = "save_recipe.php";
$submitButtonText = "Save Recipe";

include 'recipe_form.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to save form data to sessionStorage
        function saveFormData() {
            console.log('saveFormData called'); // Debugging
            const formData = {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                prep_time: document.getElementById('prep_time').value,
                cook_time: document.getElementById('cook_time').value,
                servings: document.getElementById('servings').value,
                cuisine_id: document.getElementById('cuisine_id').value,
                category_id: document.getElementById('category_id').value,
                spicy: document.getElementById('spicy').checked,
            };
            console.log('Saving form data:', formData); // Debugging
            sessionStorage.setItem('addRecipeFormData', JSON.stringify(formData));
        }

        // Function to restore form data from sessionStorage
        function restoreFormData() {
            const savedData = sessionStorage.getItem('addRecipeFormData');
            if (savedData) {
                const formData = JSON.parse(savedData);
                console.log('Restoring form data:', formData); // Debugging
                document.getElementById('title').value = formData.title || '';
                document.getElementById('description').value = formData.description || '';
                document.getElementById('prep_time').value = formData.prep_time || '';
                document.getElementById('cook_time').value = formData.cook_time || '';
                document.getElementById('servings').value = formData.servings || '';
                document.getElementById('cuisine_id').value = formData.cuisine_id || '';
                document.getElementById('category_id').value = formData.category_id || '';
                document.getElementById('spicy').checked = formData.spicy || false;
            }
        }

        // Function to clear form data from sessionStorage after successful submission
        function clearFormData() {
            console.log('Clearing form data'); // Debugging
            sessionStorage.removeItem('addRecipeFormData');
        }

        // Attach event listeners
        restoreFormData(); // Restore data on page load
        document.querySelector('form').addEventListener('input', function() {
            console.log('Input event triggered'); // Debugging
            saveFormData();
        }); // Save data on input
        document.querySelector('form').addEventListener('submit', clearFormData); // Clear data on submit
    });
</script>