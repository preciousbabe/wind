const products = {
    "TYPE 12.5" :{
        "power": "12.5 KVa / 10 KW",
        "phase": "single phase",
        "output": "220 Volts",
        "amps": "32 amp",
        "size": "2 x 2 x 4 / 150 kg"
    },
    "TYPE 25" :{
        "power": "25 KVa / 20 KW",
        "phase": "single phase",
        "output": "220 Volts",
        "amps": "65 amp",
        "size": "4 x 2 x 4 / 200 kg"
    },
    "TYPE 60" :{
        "power": "50 KVa / 40 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "60 amp",
        "size": "5 x 3 x 4 / 400 kg"
    },
    "TYPE 75" :{
        "power": "75 KVa / 60 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "90 amp",
        "size": "5 x 3 x 4 / 550 kg"
    },
    "TYPE 125" :{
        "power": "125 KVa / 100 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "120 amp",
        "size": "6 x 4 x 6 / 750 kg"
    },
    "TYPE 150" :{
        "power": "150 KVa / 125 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "180 amp",
        "size": "7 x 4 x 7 / 1100 kg"
    },
    "TYPE 250" :{
        "power": "250 KVa / 200 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "300 amp",
        "size": "7 x 4 x 7 / 1500 kg"
    },
    "TYPE 620" :{
        "power": "625 KVa / 500 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "600 amp",
        "size": "10 x 4 x 7 / 3000 kg"
    },
    "TYPE 1000" :{
        "power": "1000 KVa / 800 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "1200 amp",
        "size": "12 x 5 x 10 / 5000 kg"
    }
    
}

document.addEventListener('DOMContentLoaded', ()=>{
    const inpElem = $('#inp_product_type')[0];
    $(inpElem).html('');
    //loop through products
    for(let product in products){
        inpElem.innerHTML += `<option value="${product}">${product}</option>`;
    }
    //event listener
    $(inpElem).on('change', (e) => {
        const val = e.target.value;
        $('#power').html(products[val].power)
        $('#phase').html(products[val].phase)
        $('#output').html(products[val].output)
        $('#amps').html(products[val].amps)
        $('#dimension').html(products[val].dimension)
    })

})