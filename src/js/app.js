var sort_field = 'position';
(async function () {
    console.debug("RUN");
    loadData();
})()

async function loadData(sortby=''){
    data = await getData(sortby);
    content = getTemplate(data);
    document.getElementById('content').innerHTML = content;
    addEvents();
    console.debug('sort_field', sort_field);
}

function setSortField(field){
    if (sort_field == field) return;
    sort_field = field;
    loadData(field);
}
function addEvents(){
    var tableHeaders = document.getElementsByClassName('sort');
    console.log('tableHeaders',tableHeaders);
    for(let i=0; i < tableHeaders.length; i++){
        console.log('added');
        tableHeaders[i].addEventListener('click', function(e){
            setSortField(e.target.getAttribute('data'));
        });
    }
}
async function getData(sortby='') {
    sortby = sortby? `&sort=${sortby}` : '';
    url = `api.php?ratings${sortby}`;
    let resp = await fetch(url);
    let data = await resp.json()
    console.debug('getData', data);
    return data;
}
function getTemplate(groups) {
    let content = '';
    Object.keys(groups).forEach((category) => {
        content += getTemplateForGroup(category, groups[category]);
    })
    return content;
}

function getTemplateForGroup(group_title, items) {
    let content = '';
    content += `<h2>${group_title}</h2>`;
    sort_field
    sortable_fields = [
        {val: 'position', label: 'позиция в рейтинге'},
        {val: 'title', label: 'позиция в рейтинге'},
        {val: 'rating', label: 'позиция в рейтинге'},
        {val: 'votes', label: 'позиция в рейтинге'},
        {val: 'avg_rating', label: 'позиция в рейтинге'},
        {val: 'year', label: 'позиция в рейтинге'},
    ];
    content += `<div class="item header">`;
    sortable_fields.forEach((field)=>{
        content += `<div class="sort ${field.val == sort_field? 'sorted' : ''}" data=${field.val}>${field.label}</div>`;
    })        
    content += `</div>`;
    items.forEach((item) => {
        content += getTemplateForItem(item);
    })
    return content;
}

function getTemplateForItem(item) {
    let content = '';
    content += `<div class="item">
            <div>${item.position}</div>
            <div>${item.title}</div>
            <div>${item.rating}</div>
            <div>${item.votes}</div>
            <div>${item.avg_rating}</div>
            <div>${item.year}</div>
        </div>`;
    return content;
}