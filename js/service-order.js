document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#admin-table tbody');
    let draggedRow = null;
    let dragAllowed = false;

    // Ajout d'une zone de drop minimaliste en haut
    const dropZoneTop = document.createElement('tr');
    dropZoneTop.className = 'drop-zone-top';
    dropZoneTop.innerHTML = '<td colspan="4" style="height:0;padding:0;border-top:2px solid transparent;"></td>';
    tableBody.insertBefore(dropZoneTop, tableBody.firstChild);

    dropZoneTop.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZoneTop.firstChild.style.borderTop = '2px solid #0072ff';
    });
    dropZoneTop.addEventListener('dragleave', function(e) {
        dropZoneTop.firstChild.style.borderTop = '2px solid transparent';
    });
    dropZoneTop.addEventListener('drop', function(e) {
        e.preventDefault();
        if (draggedRow) {
            tableBody.insertBefore(draggedRow, dropZoneTop.nextSibling);
            updateServiceOrder();
        }
        dropZoneTop.firstChild.style.borderTop = '2px solid transparent';
    });

    // Drag autorisé uniquement via le grip
    tableBody.addEventListener('mousedown', function(e) {
        if (e.target.classList.contains('grip-icon')) {
            dragAllowed = true;
        } else {
            dragAllowed = false;
        }
    });
    tableBody.addEventListener('dragstart', function(e) {
        if (dragAllowed && e.target.tagName === 'TR') {
            draggedRow = e.target;
            e.dataTransfer.effectAllowed = 'move';
        } else {
            e.preventDefault();
        }
    });
    tableBody.addEventListener('dragend', function(e) {
        dragAllowed = false;
    });

    tableBody.addEventListener('dragover', function(e) {
        e.preventDefault();
        const target = e.target.closest('tr');
        if (target && target !== draggedRow) {
            const rect = target.getBoundingClientRect();
            const offset = e.clientY - rect.top;
            if (offset < rect.height / 2) {
                target.style.borderTop = '2px solid #0072ff';
                target.style.borderBottom = '';
            } else {
                target.style.borderTop = '';
                target.style.borderBottom = '2px solid #0072ff';
            }
        }
    });

    tableBody.addEventListener('dragleave', function(e) {
        const target = e.target.closest('tr');
        if (target) {
            target.style.borderTop = '';
            target.style.borderBottom = '';
        }
    });

    tableBody.addEventListener('drop', function(e) {
        e.preventDefault();
        const target = e.target.closest('tr');
        if (target && draggedRow && target !== draggedRow) {
            const rect = target.getBoundingClientRect();
            const offset = e.clientY - rect.top;
            target.style.borderTop = '';
            target.style.borderBottom = '';
            if (offset < rect.height / 2) {
                tableBody.insertBefore(draggedRow, target);
            } else {
                tableBody.insertBefore(draggedRow, target.nextSibling);
            }
            updateServiceOrder();
        }
    });

    function updateServiceOrder() {
        const ids = Array.from(tableBody.querySelectorAll('tr')).map(tr => tr.getAttribute('data-id'));
        fetch('index.php?page=services&action=updateOrder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ order: ids })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Optionnel : afficher un message de succès
            }
        });
    }
});
