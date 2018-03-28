const req = require('request');

// init analyisis bitcoin heuristic from 515000 to 0 blockchain blocks

global.current_block = 515000
global.current_threads = 0

// max threads connection
const MAX_THREADS = 15
var ls = []

setInterval(function() {
    if (MAX_THREADS > global.current_threads) {
        global.current_threads++
            global.current_block--
            console.log("Baixando o bloco: ", global.current_block)

        req("http://127.0.0.1/index.php/welcome/index/" + global.current_block, function(error, response, body) {

            global.current_threads--
                if (error) {
                    console.log(error)
                    console.log("Ocorreu um erro ao baixar o bloco: ", global.current_block)
                }
        })
    }
}, 1);