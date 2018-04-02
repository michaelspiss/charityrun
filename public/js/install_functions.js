function emptyString() {
    for(var i = 0; i<arguments.length; i++) {
        if(!arguments[i] || arguments[i] === "" || arguments[i] === null) {
            return true;
        }
    }
    return false;
}
function resetMapCoordinates()
{
    document.getElementById('latitude_from').setAttribute('value', '');
    document.getElementById('longitude_from').setAttribute('value', '');
    document.getElementById('latitude_to').setAttribute('value', '');
    document.getElementById('longitude_to').setAttribute('value', '');
}

var lines_count = 0;
function addMapCoordinates(lat_from, long_from, lat_to, long_to)
{
    map.dataProvider.lines.push({
        "id": "line_"+lines_count,
        "latitudes": [lat_from, lat_to],
        "longitudes": [long_from, long_to],
        'title': lines_count
    });
    map.validateData();
}

var lines = {};
function addLine()
{
    lat_from = document.getElementById('latitude_from').value;
    long_from = document.getElementById('longitude_from').value;
    lat_to = document.getElementById('latitude_to').value;
    long_to = document.getElementById('longitude_to').value;
    if(!emptyString(lat_from, long_from, lat_to, long_to))
    {
        lines["line_"+lines_count] = {
            'lat_from': lat_from,
            'long_from': long_from,
            'lat_to': lat_to,
            'long_to': long_to
        };
        document.getElementById('lines_input').setAttribute('value', JSON.stringify(getLines()));
        addMapCoordinates(lat_from, long_from, lat_to, long_to);
        addLineToTable(lat_from, long_from, lat_to, long_to);
        // reset only "to" coordinates
        document.getElementById('latitude_to').value = '';
        document.getElementById('longitude_to').value = '';
        lines_count++;
    }
}

function addLineToTable(lat_from, long_from, lat_to, long_to)
{
    var tr = document.getElementById('new_table_row').cloneNode(true);
    tr.setAttribute('id', 'table_line_'+lines_count);
    tr.children[0].innerHTML = lines_count;
    tr.children[1].innerHTML = lat_from;
    tr.children[2].innerHTML = long_from;
    tr.children[3].innerHTML = lat_to;
    tr.children[4].innerHTML = long_to;
    tr.children[5].children[0].setAttribute('data-line-id', lines_count);
    document.getElementById('lines_table').appendChild(tr);
}

function getLines() {
    var values = [];

    for (var k in lines) {
        if (lines.hasOwnProperty(k)) {
            values.push(lines[k]);
        }
    }

    return values;
}

function removeLine(button)
{
    var id = button.getAttribute('data-line-id');
    if(lines["line_"+id] !== undefined) {
        delete lines['line_'+id];
        document.getElementById('table_line_'+id).remove();
        document.getElementById('lines_input').setAttribute('value', JSON.stringify(getLines()));
        // remove from map
        var index = map.dataProvider.lines.findIndex(function (sub) {
            return sub.id === 'line_'+id;
        });
        map.dataProvider.lines.splice(index, 1);
        map.validateData();
    }
}