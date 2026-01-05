/**
 * Global Table Sorting Function
 * @param {string} tableId - ID of the table to sort
 * @param {number} n - Column index (0-based)
 * @param {string} type - Sort type ('string', 'number', 'grade', 'role', 'date', 'none')
 */
function sortTable(tableId, n, type) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById(tableId);
    if (!table) return;
    switching = true;
    dir = "asc";

    // Icon management
    $(table).find('th i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
    var th = table.getElementsByTagName("TH")[n];

    while (switching) {
        switching = false;
        rows = table.rows;

        // Loop through all table rows (except the first, which contains table headers):
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];

            var xVal = $(x).text().trim();
            var yVal = $(y).text().trim();

            // Extract custom values if present (data-value attribute)
            if ($(x).find('[data-value]').length > 0) xVal = $(x).find('[data-value]').data('value');
            if ($(y).find('[data-value]').length > 0) yVal = $(y).find('[data-value]').data('value');

            // Handle Types
            if (type == 'grade') {
                xVal = getGradeRank(xVal);
                yVal = getGradeRank(yVal);
            } else if (type == 'role') {
                xVal = getRoleRank(xVal);
                yVal = getRoleRank(yVal);
            } else if (type == 'number') {
                xVal = parseFloat(xVal) || 0;
                yVal = parseFloat(yVal) || 0;
            } else if (type == 'date') {
                xVal = new Date(xVal).getTime();
                yVal = new Date(yVal).getTime();
            } else if (type == 'none') {
                continue;
            } else {
                xVal = xVal.toLowerCase();
                yVal = yVal.toLowerCase();
            }

            if (dir == "asc") {
                if (xVal > yVal) { shouldSwitch = true; break; }
            } else if (dir == "desc") {
                if (xVal < yVal) { shouldSwitch = true; break; }
            }
        }

        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }

    // Update Icon
    var icon = $(th).find('i');
    icon.removeClass('fa-sort');
    if (dir == "asc") icon.addClass('fa-sort-up');
    else icon.addClass('fa-sort-down');
}

/**
 * Helper: Rank academic grades
 */
function getGradeRank(grade) {
    if (!grade) return 99;
    grade = grade.toLowerCase();
    if (grade.includes('prof')) return 1;
    if (grade.includes('mca')) return 2;
    if (grade.includes('mcb')) return 3;
    if (grade.includes('maa')) return 4;
    if (grade.includes('mab')) return 5;
    if (grade.includes('doctorant')) return 6;
    return 99;
}

/**
 * Helper: Rank roles
 */
function getRoleRank(role) {
    if (!role) return 99;
    role = role.toLowerCase();
    if (role.includes('chef')) return 1;
    if (role.includes('admin')) return 2;
    if (role.includes('enseignant')) return 3;
    if (role.includes('chercheur')) return 4;
    if (role.includes('membre')) return 5;
    if (role.includes('doctorant')) return 6;
    if (role.includes('etudiant')) return 7;
    return 99;
}
