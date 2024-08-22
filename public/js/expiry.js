import { manageDealerInfo } from './dealer.js'
import { manageProductInfo } from './product.js'
import {
    createElement,
    activateFormBtn,
    closeErrorBox
} from './primaryFormOperations.js'

let productCloseImg = document.getElementById('product-close-img')
let productSelectBtn = document.getElementById('product-select-button')
let stockTable = document.getElementById('stock-table')
let totalStock = document.getElementById('totalStock')
let totalTax = document.getElementById('totalTax')
let totalEntries = document.getElementById('stockEntries')
let totalStockAmount = document.getElementById('stockAmt')

manageProductInfo('stock')
function displaySelected (e, d, stockEntryRelation = null) {
    let p = e.parentElement.parentElement
    p.children[0].children[0].value = d[0].id
    p.children[1].children[0].value = d[0].product_id
    p.children[2].children[0].value = d[0].product_name
    if (stockEntryRelation == null) {
        p.children[3].children[0].value = d[0].quantity
        p.children[4].children[0].value = d[0].rate
        p.children[5].children[0].value = d[0].MRP
        p.children[6].children[0].value = d[0].GST
        p.children[7].children[0].value = d[0].GST / 2
        p.children[8].children[0].value = d[0].GST / 2
    } else {
        p.children[3].children[0].value = stockEntryRelation.quantity
        p.children[4].children[0].value = stockEntryRelation.rate
        p.children[5].children[0].value = stockEntryRelation.MRP
        p.children[6].children[0].value = stockEntryRelation.GST
        p.children[7].children[0].value = stockEntryRelation.GST / 2
        p.children[8].children[0].value = stockEntryRelation.GST / 2
        p.children[9].children[0].value = stockEntryRelation.expDate
    }
    e.parentElement.parentElement.children[3].children[0].focus()
    p.children[1].children[0].setAttribute('readonly', true)
}

function performEditing () {
    let sum = 0
    let count = 0
    let quantityEle = document.querySelectorAll('.quantity')
    quantityEle.forEach(e => {
        let v = parseInt(e.value)
        if (isNaN(v)) v = 0
        sum += v
        count++
    })
    let mul = 0
    let tax = 0
    for (let i = 0; i < quantityEle.length; i++) {
        let value = parseInt(quantityEle[i].value)
        let p = quantityEle[i].parentElement.parentElement
        let rate = parseInt(p.children[4].children[0].value)
        let taxRate = parseInt(p.children[6].children[0].value)
        let CGST = p.children[7].children[0]
        let SGST = p.children[8].children[0]
        let amt = p.children[10].children[0]

        if (isNaN(value)) value = 0
        if (isNaN(rate)) rate = 0
        if (isNaN(taxRate) || taxRate == 0) continue
        else {
            CGST.value = taxRate / 2
            SGST.value = taxRate / 2
        }
        tax = tax + (rate * value * taxRate) / 100
        amt.value = (rate * value).toFixed(2)
        mul = mul + parseFloat(amt.value)
    }

    if (tax == 'infinity') totalTax.value = 0
    else totalTax.value = tax.toFixed(2)

    totalStockAmount.value = mul.toFixed(2)
    totalStock.value = sum.toFixed(2)
    totalEntries.value = count
}

function selectingSpecifiedProduct () {
    let productId = document.querySelectorAll('.productId')
    productSelectBtn.addEventListener('click', event => {
        event.preventDefault()
        let selectedProductRow = document.getElementById('selectedProductRow')
        let e = document.getElementById('clickedRow')
        if (e != null) {
            let obj = [
                {
                    id: selectedProductRow.children[0].textContent,
                    product_id: selectedProductRow.children[1].textContent,
                    product_name: selectedProductRow.children[4].textContent,
                    quantity: 1,
                    rate: selectedProductRow.children[6].textContent,
                    MRP: selectedProductRow.children[7].textContent,
                    GST: selectedProductRow.children[9].textContent
                }
            ]

            displaySelected(e, obj)
            productCloseImg.click()
            e.removeAttribute('id')
            performEditing()
            productSelectBtn.setAttribute('disabled', true)
        }
    })

    productId.forEach(e => {
        e.addEventListener('keyup', async event => {
            event.preventDefault()
            if (event.key == 'Enter') {
                let req = await fetch(`product/${e.value.trim()}`)
                let data = req.json()
                data.then(d => {
                    if ('error' in d[0]) {
                        alert(d[0].error)
                    } else {
                        displaySelected(e, d)
                    }
                })
            }
            if (
                event.key == 'q' &&
                event.altKey &&
                !e.hasAttribute('readonly')
            ) {
                document
                    .getElementById('product-container')
                    .classList.remove('d-none')
                e.setAttribute('id', 'clickedRow')
                productId.forEach(ele => {
                    if (ele.getAttribute('id') != 'clickedRow')
                        ele.removeAttribute('id')
                })
                document.getElementById('search-product').focus()
            }
        })
    })

    let gst = document.querySelectorAll('.gst')
    gst.forEach(e => {
        e.addEventListener('keyup', event => {
            event.preventDefault()
            let p = e.parentElement.parentElement
            if (
                event.key == 'Enter' &&
                sessionStorage.getItem('preventForRemoving') == null
            ) {
                let date = p.children[9].children[0].value
                let v = p.children[0].children[0].value
                if (v != '' && v != null) {
                    if (date == '' || date == null) {
                        alert('Please! Select Expiry Date..')
                    } else {
                        if (
                            v != null &&
                            v != '' &&
                            p.nextElementSibling == null
                        ) {
                            createElement('expiry')
                        } else {
                            p.nextElementSibling.children[1].children[0].focus()
                        }
                    }
                }
            }
        })
    })

    let tableRows = document.querySelectorAll('.stock-element')
    tableRows.forEach(e => {
        e.addEventListener('keyup', event => {
            event.preventDefault()
            if (
                event.key == 'q' &&
                event.ctrlKey &&
                sessionStorage.getItem('preventForRemoving') == null
            ) {
                let parent = e.parentElement.parentElement
                parent.remove()
                let l = document.querySelectorAll('.stock-element').length
                if (l == 0) {
                    createElement('expiry')
                }
                performEditing()
            }
        })
    })

    let dates = document.querySelectorAll('.dates')
    dates.forEach(ele => {
        ele.addEventListener('keypress', e => {
            if (e.key == 'Enter') {
                e.preventDefault()
            }
        })
    })

    let editableFields = document.querySelectorAll('.editable-fields')
    editableFields.forEach(edit => {
        edit.addEventListener('keyup', e => {
            e.preventDefault()
            if (e.key == 'Enter') {
                performEditing()
            }
        })
        edit.addEventListener('mousemove', e => {
            e.preventDefault()
            performEditing()
        })
    })
}

selectingSpecifiedProduct()
let observe = new MutationObserver(() => {
    selectingSpecifiedProduct()
})
observe.observe(stockTable, { childList: true })
window.onload = () => {
    createElement('expiry')
    sessionStorage.clear()
}

activateFormBtn()
//selecting specified dealer
manageDealerInfo()
let entryError = document.getElementById('entryNoError')
let stockForm = document.getElementById('expiryEntryForm')
let method = document.getElementById('method')

let entry = document.getElementById('entryNumber')
//sending request to controller
entry.addEventListener('keyup', e => {
    e.preventDefault()
    if (e.key == 'Enter') {
        fetch(`expiry/${entry.value.trim()}/${date.value.trim()}`)
            .then(res => res.json())
            .then(data => {
                if ('error' in data[0]) {
                    alert(data[0].error)
                } else {
                    let dealerEle = document.querySelectorAll('.dealer')
                    if (data[0].dealer != null) {
                        dealerEle[0].value = data[0].dealer.id
                        dealerEle[1].value = data[0].dealer.dealer_name
                        dealerEle[2].value = data[0].dealer.contact
                        dealerEle[3].value = data[0].dealer.email
                        dealerEle[4].value = data[0].dealer.GST_no
                    }
                    document.querySelectorAll('.stockTableRow').forEach(e => {
                        e.remove()
                    })
                    data[0].product.forEach(element => {
                        createElement('expiry')
                        let created = document.getElementById('parent')
                        let relationalView = {
                            rate: element.pivot.rate,
                            MRP: element.pivot.MRP,
                            GST: element.pivot.GST,
                            quantity: element.pivot.returnQuantity,
                            expDate: element.pivot.expiry_date
                        }
                        displaySelected(created, [element], relationalView)
                        created.removeAttribute('id')
                    })
                    performEditing()
                    stockForm.setAttribute(
                        'action',
                        `http://localhost:8000/expiry/${entry.value.trim()}/${date.value.trim()}`
                    )
                    let input = document.createElement('input')
                    input.setAttribute('name', '_method')
                    input.setAttribute('type', 'hidden')
                    if (sessionStorage.getItem('update') != null) {
                        input.setAttribute('value', 'PUT')
                    } else {
                        input.setAttribute('value', 'DELETE')
                    }
                    method.innerHTML = ''
                    method.append(input)
                    sessionStorage.setItem('preventForRemoving', 'update')
                }
            })
    }
})

document.addEventListener('contextmenu', e => {
    e.preventDefault()
})

closeErrorBox()
