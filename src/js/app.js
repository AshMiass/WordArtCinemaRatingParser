var sort_field = 'position';
var loaded_data = [];
(async function () {
    console.debug("RUN");
    loadData();
})()

function openModal(e){
    data = e.target.parentNode.parentNode.getAttribute('data');
    console.log('openModal', data)
    setModal(data);
}

function setModal(film_id){
     var modal = document.getElementById('modal');
     var span = document.getElementsByClassName("close")[0];
     modal.style.display = "block";
     var modalContentElement = document.getElementById('modalContent');
     item = loaded_data[film_id];
     content = getTemplateForItem(item);
     content += `<div>${item.short_description?? ''}</div>`
     modalContentElement.innerHTML = content;
     // When the user clicks on <span> (x), close the modal
     span.onclick = function() {
         modal.style.display = "none";
         modalContentElement.innerHTML = '';
     }
 
     // When the user clicks anywhere outside of the modal, close it
     window.onclick = function(event) {
         if (event.target == modal) {
            modalContentElement.innerHTML = '';
            modal.style.display = "none";
         }
     }
}

function setDate(e){
    console.debug('setDate',e.target.value)
    loadData(sort_field, e.target.value);
}

async function loadData(sortby='', filter=''){
    data = await getData(sortby, filter);
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
async function getData(sortby='', date='') {
    sortby = sortby? `&sort=${sortby}` : '';
    date = date? `&date=${date}` : '';
    url = `entry.php?ratings${sortby}${date}`;
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
        content += `<div class="sort ${field.val == sort_field? 'sorted' : ''}">${field.label}</div>`;
       
    })        
    content += `</div>`;
    items.forEach((item) => {
        loaded_data[item.film_id] = item;
        content += getTemplateForItem(item);
    })
    if (!items.length) content += 'на заданную дату данных нет'
    return content;
}

function getTemplateForItem(item) {
    let content = '';
    content += `<div class="item" data="${item.film_id}">
            <div>${item.position}</div>
            <div class='film_title'><span onclick="openModal(event)">${item.title}<span></div>
            <div>${item.rating}</div>
            <div>${item.votes}</div>
            <div>${item.avg_rating}</div>
            <div>${item.year}</div>
        </div>`;
    return content;
}