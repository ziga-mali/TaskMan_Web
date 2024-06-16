function MD5hash(geslo){
    hashGeslo = CryptoJS.MD5(geslo).toString();
    return hashGeslo;
}


function getCookie(name) {
    const cookieValue = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
    return cookieValue ? cookieValue.pop() : '';
}

function limitCheckboxSelection(listId) {
    const list = document.getElementById(listId);
    list.addEventListener('change', function(event) {
        if (event.target.type === 'checkbox' && event.target.checked) {
            const checkboxes = list.querySelectorAll(`input[name="${event.target.name}"]`);
            checkboxes.forEach(checkbox => {
                if (checkbox !== event.target) {
                    checkbox.checked = false;
                }
            });
        }
    });
}

function getCheckedItemId(listId) {
    const list = document.getElementById(listId);
    const checkedItem = list.querySelector('input[type="checkbox"]:checked');
    return checkedItem ? checkedItem.value : null;
}

function formatDateForInput(datetimeString) {
    const date = new Date(datetimeString);
    const year = date.getFullYear();
    const month = ('0' + (date.getMonth() + 1)).slice(-2);
    const day = ('0' + date.getDate()).slice(-2);
    const hours = ('0' + date.getHours()).slice(-2);
    const minutes = ('0' + date.getMinutes()).slice(-2);
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}