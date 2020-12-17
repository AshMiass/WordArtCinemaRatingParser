var sort_field = 'position';
var loaded_data = new Map();
var loaded_films = [];
const api_entry_point = 'entry.php';
(async function () {
    console.debug("RUN");
    loadData(sort_field);
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
     item = loaded_films[film_id];
     content = getTemplateForItem(item);
     content += `<div class="description">${item.short_description?? ''}</div>`
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

async function loadData(sortby = '', filter=''){
    data = await getData(sortby, filter);
    content = getTemplate(data);
    document.getElementById('content').innerHTML = content;
    addEvents();
}

function setSortField(field){
    if (sort_field == field) return;
    sort_field = field;
    loadData(field);
}
function addEvents(){
    var tableHeaders = document.getElementsByClassName('sort');
    for(let i=0; i < tableHeaders.length; i++){
        tableHeaders[i].addEventListener('click', function(e){
            setSortField(e.target.getAttribute('data'));
        });
    }
}
async function getData(sortby='', date='') {
    sortby = sortby? `&sort=${sortby}` : '';
    date = date? `&date=${date}` : '';
    params = `${sortby}${date}`.toString();
    url = `${api_entry_point}?ratings${params}`;
    console.log('loaded_data.has(params)', params, loaded_data.has(params));
    if (loaded_data.has(params)) return loaded_data.get(params);
    let resp = await fetch(url);
    let data = await resp.json()
    console.debug('getData', loaded_data);
    loaded_data.set(params, data);
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
    header_fields = [
        {val: 'position', label: 'позиция в рейтинге'},
        {val: '', label: ''},
        {val: 'title', label: 'название'},
        {val: 'rating', label: 'расчетный балл'},
        {val: 'votes', label: 'голоса'},
        {val: 'avg_rating', label: 'средний балл'},
        {val: 'year', label: 'год'},
    ];
    content += `<div class="item header">`;
    header_fields.forEach((field)=>{
        content += `<div class="${field.val? 'sort' : ''} ${field.val == sort_field? 'sorted' : ''}" data="${field.val}">${field.label}</div>`;
       
    })        
    content += `</div>`;
    items.forEach((item) => {
        loaded_films[item.film_id] = item;
        content += getTemplateForItem(item);
    })
    if (!items.length) content += 'на заданную дату данных нет'
    return content;
}

function getTemplateForItem(item) {
    let content = '';
    poster_el = (item.file_path && item.file_path != 'none')? `<img src="${item.file_path}" class="poster" />` : 'нет постера';
    content += `<div class="item" data="${item.film_id}">
            <div>${item.position}</div>`;
    content += `<div>${poster_el}</div>`;
    content += `<div class='film_title'><span onclick="openModal(event)">${item.title}<span></div>
            <div>${item.rating}</div>
            <div>${item.votes}</div>
            <div>${item.avg_rating}</div>
            <div>${item.year}</div>
        </div>`;
    return content;
}