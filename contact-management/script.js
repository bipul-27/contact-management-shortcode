document.addEventListener('DOMContentLoaded', function() {
    fetchContacts();
});

async function fetchContacts() {
    const response = await fetch(ajax_object.ajax_url + '?action=get_contacts');
    const data = await response.json();
    const contactsTable = document.getElementById('contacts');
    contactsTable.innerHTML = '';
    data.data.forEach((contact) => {
        contactsTable.innerHTML += `
            <tr>
                <td>${contact.name}</td>
                <td>${contact.email}</td>
                <td>${contact.phone}</td>
                <td>${contact.gender}</td>
                <td>${contact.designation}</td>
                <td class="actions">
                    <button class="edit" onclick="showEditForm(${contact.id}, '${contact.name}', '${contact.email}', '${contact.phone}', '${contact.gender}', '${contact.designation}')">Edit</button>
                    <button class="delete" onclick="deleteContact(${contact.id})">Delete</button>
                </td>
            </tr>
        `;
    });
    adjustContainerWidth();
}

function showEditForm(id, name, email, phone, gender, designation) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-phone').value = phone;
    document.getElementById('edit-gender').value = gender;
    document.getElementById('edit-designation').value = designation;

    document.getElementById('contact-form').style.display = 'none';
    document.getElementById('edit-form').style.display = 'block';
}

async function addContact() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const gender = document.getElementById('gender').value;
    const designation = document.getElementById('designation').value;

    let valid = true;

    if (name === '') {
        document.getElementById('name-error').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('name-error').style.display = 'none';
    }

    if (email === '') {
        document.getElementById('email-error').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('email-error').style.display = 'none';
    }

    if (phone === '') {
        document.getElementById('phone-error').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('phone-error').style.display = 'none';
    }

    if (!valid) {
        return;
    }

    const response = await fetch(ajax_object.ajax_url + '?action=add_contact', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `name=${name}&email=${email}&phone=${phone}&gender=${gender}&designation=${designation}`
    });

    if (response.ok) {
        showSuccessMessage();
        document.getElementById('name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('gender').value = '';
        document.getElementById('designation').value = '';
        setTimeout(() => {
            window.location.reload();
        }, 2000); // Refresh the page after 2 seconds
    }
}

async function updateContact() {
    const id = document.getElementById('edit-id').value;
    const name = document.getElementById('edit-name').value;
    const email = document.getElementById('edit-email').value;
    const phone = document.getElementById('edit-phone').value;
    const gender = document.getElementById('edit-gender').value;
    const designation = document.getElementById('edit-designation').value;

    let valid = true;

    if (name === '') {
        document.getElementById('edit-name-error').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('edit-name-error').style.display = 'none';
    }

    if (email === '') {
        document.getElementById('edit-email-error').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('edit-email-error').style.display = 'none';
    }

    if (phone === '') {
        document.getElementById('edit-phone-error').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('edit-phone-error').style.display = 'none';
    }

    if (!valid) {
        return;
    }

    const response = await fetch(ajax_object.ajax_url + '?action=edit_contact', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${id}&name=${name}&email=${email}&phone=${phone}&gender=${gender}&designation=${designation}`
    });

    if (response.ok) {
        showSuccessMessage();
        document.getElementById('edit-form').style.display = 'none';
        document.getElementById('contact-form').style.display = 'block';
        fetchContacts();
    }
}

async function deleteContact(id) {
    const response = await fetch(ajax_object.ajax_url + '?action=delete_contact', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${id}`
    });

    if (response.ok) {
        fetchContacts();
    }
}

function showSuccessMessage() {
    const successMessage = document.getElementById('success-message');
    successMessage.style.display = 'block';
    setTimeout(() => {
        successMessage.style.display = 'none';
    }, 1000);
}
function adjustContainerWidth() {
    const container = document.querySelector('.contact-management');
    const table = document.querySelector('.contact-management table');
    if (table && container) {
        container.style.width = table.offsetWidth + 'px';
    }
}
