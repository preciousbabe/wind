const products = {
    "TYPE 10" :{
        "power": "10 KVa / 8 KW",
        "phase": "single phase",
        "output": "220 Volts",
        "amps": "25 amp",
        "size": "122 x 61 x 91 / 150 kg"
    },
    "TYPE 15" :{
        "power": "15 KVa / 12 KW",
        "phase": "single phase",
        "output": "220 Volts",
        "amps": "65 amp",
        "size": "122 x 61 x 91 / 200 kg"
    },
    "TYPE 20" :{
        "power": "20 KVa / 16 KW",
        "phase": "single phase",
        "output": "220 Volts",
        "amps": "90 amp",
        "size": "122 x 61 x 121 / 250 kg"
    },
    "TYPE 25" :{
        "power": "25 KVa / 20 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "42 amp",
        "size": "122 x 61 x 121 / 350 kg"
    },
    "TYPE 50" :{
        "power": "50 KVa / 40 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "60 amp",
        "size": "153 x 92 x 91 / 400 kg"
    },
    "TYPE 75" :{
        "power": "75 KVa / 60 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "90 amp",
        "size": "153 x 92 x 91 / 550 kg"
    },
    "TYPE 100" :{
        "power": "100 KVa / 80 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "120 amp",
        "size": "183 x 122 x 183 / 750 kg"
    },
    "TYPE 150" :{
        "power": "150 KVa / 120 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "180 amp",
        "size": "214 x 122 x 214 / 1100 kg"
    },
    "TYPE 250" :{
        "power": "250 KVa / 200 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "300 amp",
        "size": "214 x 122 x 214 / 1500 kg"
    },
    "TYPE 500" :{
        "power": "500 KVa / 400 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "600 amp",
        "size": "305 x 122 x 214 / 3000 kg"
    },
    "TYPE 1000" :{
        "power": "1000 KVa / 800 KW",
        "phase": "Three phase",
        "output": "480 Volts",
        "amps": "1200 amp",
        "size": "366 x 153 x 305 / 5000 kg"
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
        $('#size').html(products[val].size)
    })

})