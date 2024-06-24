self.onmessage = function (event) {
    console.log('interval refresh : ', event.data);

    setInterval(function () {
        postMessage('');
    }, event.data);
};