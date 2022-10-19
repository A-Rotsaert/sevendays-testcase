document.getElementById('delete_child').addEventListener('click', function () {
    if (confirm(this.dataset.deleteMessage)) {
        location.replace(this.dataset.deleteUrl);
    }
})

