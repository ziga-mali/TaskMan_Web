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