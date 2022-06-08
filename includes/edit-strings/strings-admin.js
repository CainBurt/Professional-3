function updateString(string, replacement) {
    return new Promise((res,rej) => {
        fetch(`${ajaxurl}?action=update_edit_strings&string=${string}&replacement=${replacement}`)
            .then(r => r.json())
            .then(r => {
                if (r.success) {
                    location.reload()
                } else {
                    rej()
                }
            })
    })
}

function stringFormSubmitHandler(e) {
    e.preventDefault();
    const formData = new FormData(this);
    this.closest('tr').style.opacity = '0.3';
    this.closest('tr').style.pointerEvents = 'none';
    updateString(formData.get('string'),formData.get('replacement'))
        .catch(() => {
            this.closest('tr').style.opacity = '';
            this.closest('tr').style.pointerEvents = '';
            this.insertAdjacentHTML('beforeend', '<p style="color:red;">There was an error updating. Refresh the page and try again.</p>')
        });
}

function editStringsInit() {
    let stringForms = [].slice.call(document.querySelectorAll('form.string-edit-form'))

    if (stringForms) {
        stringForms.forEach(form => {
            form.addEventListener('submit', stringFormSubmitHandler, false);
        })
    }
}

document.addEventListener('DOMContentLoaded', editStringsInit, false);