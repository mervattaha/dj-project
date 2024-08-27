// ajax.js

function sendAjaxRequest(url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
    xhr.onload = function() {
        if (xhr.status === 200) {
            callback(JSON.parse(xhr.responseText));
        } else {
            console.error('Request failed. Returned status of ' + xhr.status);
        }
    };
    xhr.send(JSON.stringify(data));
}

