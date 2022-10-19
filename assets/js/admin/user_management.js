import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons-dt';
import '/assets/js/datatables/bootstrap5.js';

const target = document.querySelector('#user-management-list');
const target_url = target.dataset.targetUrl;
const moment = require('moment');
document.addEventListener("DOMContentLoaded", function () {
    new DataTable('#user-management-list', {
        order: [0, 'desc'],
        columns: [
            {data: 'id'},
            {
                data: 'name',
                render: function (data) {
                    if (data === undefined) {
                        return 'Null';
                    }
                    return data;
                }
            },
            {data: 'email'},
            {
                data: 'roles',
                render: function (data) {
                    if (data.length === 0) {
                        data.push('ROLE_USER');
                    }
                    data.join(', ');

                    return data;
                }
            },
            {
                data: 'createdAt',
                render: function (data) {
                    return moment(data).format('DD/MM/YYYY HH:MM');
                }
            },
            {
                data: 'updatedAt',
                render: function (data) {
                    return moment(data).format('DD/MM/YYYY HH:MM');
                }
            },
            {
                data: null,
                render: function (data) {
                    return "<a class='btn btn-outline-primary' href='user/" + data.id + "/edit'><i class='fas fa-edit'></i></a>&nbsp;<button class='btn btn-outline-danger delete_user' data-id='" + data.id + "'><i class='fas fa-trash-alt'></i></button>";
                },
            }
        ],
        serverSide: true,
        ajax: {
            url: target_url,
            type: "POST"
        },
        paging: true,
        info: true,
        pageLength: 10,
        initComplete: function () {
            const deleteButtons = document.getElementsByClassName('delete_user');
            for (const button of deleteButtons) {
                button.addEventListener('click', function (e) {
                    if (confirm('Are you sure to delete this user ?')) {
                        const id = button.dataset.id;
                        const url = "user/" + id + "/delete";
                        $.ajax({
                            type: "DELETE",
                            url: url,
                            success: function () {
                                location.reload();
                            },
                            error: function (xhr) {
                                console.log(xhr);
                            }
                        });
                    }
                })
            }
        }
    });
});