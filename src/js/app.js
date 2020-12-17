(async function(){
    console.log("RUN");
    data = await getData();
    content = getListTemplate(data);
    document.getElementById('content').innerHTML = content;
})()
async function getData(){
    url = "api.php";
    let resp = await fetch(url);
    let data = await resp.json()
    console.log('getData', data);
    return data;
}
function getListTemplate(groups){
    let content = 'Data loaded. Got ' + Object.keys(groups).length + ' category';
    return content;
}