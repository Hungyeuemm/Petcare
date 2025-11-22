document.addEventListener("DOMContentLoaded", () => {
    const selectAll = document.getElementById("select-all");
    const checkboxes = document.querySelectorAll('input[name="selected[]"]');
    const deleteBtn = document.getElementById("delete-selected");
    const form = document.getElementById("delete-form");

    if (selectAll) {
        selectAll.addEventListener("change", () => {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    }

    if (deleteBtn) {
        deleteBtn.addEventListener("click", () => {
            const selected = [...checkboxes].filter(cb => cb.checked);
            if (selected.length === 0) {
                alert("Chưa chọn dịch vụ nào để xóa!");
                return;
            }
            if (confirm(`Xóa ${selected.length} dịch vụ này?`)) {
                form.submit();
            }
        });
    }
});
