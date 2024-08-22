export function createElement (entry = null) {
    let stockTable = document.getElementById('stock-table')

    let tr = document.createElement('tr')
    tr.classList.add('stockTableRow')
    let len = null
    if (entry == null || entry == 'expiry') len = 12
    else len = 14
    for (let i = 0; i < len; i++) {
        let td = document.createElement('td')
        let input = document.createElement('input')
        input.setAttribute('type', 'text')
        input.classList.add('form-control', 'stock-element')
        if (i == 0) {
            input.setAttribute('name', 'pId[]')
            td.classList.add('d-none')
        }
        if (i == 1) {
            input.classList.add('productId')
            input.setAttribute('name', 'productId[]')
            input.setAttribute('id', 'parent')
        }
        if (i == 2) {
            input.setAttribute('name', 'productName[]')
            input.setAttribute('readonly', true)
        }

        if (i == 3) {
            input.setAttribute('name', 'qty[]')
            input.classList.add('editable-fields', 'quantity')
        }

        if (i == 4) {
            if (entry == 'expiry') continue
            input.setAttribute('name', 'purchase_rate[]')
            input.classList.add('editable-fields')
        }
        if (i == 5) {
            input.setAttribute('name', 'rate[]')
            input.classList.add('editable-fields')
        }

        if (i == 6) {
            input.setAttribute('name', 'mrp[]')
            input.classList.add('editable-fields')
        }

        if (i == 7) {
            input.setAttribute('name', 'gst[]')
            input.classList.add('gst', 'editable-fields')
        }
        if (i == 8 || i == 9 || i == 10 || i == 11) {
            input.setAttribute('readonly', true)
        }

        if (i == 12) {
            input.setAttribute('name', 'mfdDate[]')
            input.setAttribute('type', 'date')
            input.classList.add('dates')
        }

        if (i == 13 || (i == 10 && entry == 'expiry')) {
            input.setAttribute('name', 'expDate[]')
            input.setAttribute('type', 'date')
            input.classList.add('expDate', 'dates')
            if (i == 10) {
                input.removeAttribute('readonly')
            }
        }

        td.append(input)
        tr.append(td)
    }
    stockTable.append(tr)
    tr.children[1].children[0].focus()
}

export function activateFormBtn () {
    //activating the form buttons
    let btn = document.querySelectorAll('.button')
    let date = document.getElementById('date')
    let entry = document.getElementById('entryNumber')
    btn[0].addEventListener('click', e => {
        e.preventDefault()
        window.location.reload()
    })
    btn[1].addEventListener('click', e => {
        e.preventDefault()
        btn[0].setAttribute('disabled', true)
        btn[2].setAttribute('disabled', true)
        if (sessionStorage.getItem('update') != null)
            sessionStorage.removeItem('update')
        sessionStorage.setItem('delete', 'delete')
        entry.removeAttribute('readonly')
        entry.focus()
        date.removeAttribute('readonly')
    })
    btn[2].addEventListener('click', e => {
        e.preventDefault()
        btn[0].setAttribute('disabled', true)
        btn[1].setAttribute('disabled', true)
        if (sessionStorage.getItem('delete') != null)
            sessionStorage.removeItem('delete')
        sessionStorage.setItem('update', 'update')
        entry.removeAttribute('readonly')
        date.removeAttribute('readonly')
        entry.focus()
    })

    btn[4].addEventListener('click', e => {
        e.preventDefault()
        btn[0].removeAttribute('disabled')
        btn[1].removeAttribute('disabled')
        btn[2].removeAttribute('disabled')
        if (sessionStorage.getItem('delete') != null)
            sessionStorage.removeItem('delete')
        if (sessionStorage.getItem('update') != null)
            sessionStorage.removeItem('update')
        entry.setAttribute('readonly', true)
        date.setAttribute('readonly', true)
        sessionStorage.removeItem('preventForRemoving')
    })
}

export function closeErrorBox () {
    let errorClose = document.getElementById('close-btn')
    errorClose.addEventListener('click', e => {
        e.preventDefault()
        errorClose.parentElement.classList.add('d-none')
    })
}
