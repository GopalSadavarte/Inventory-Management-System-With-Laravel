import { manageProductInfo } from './product.js'
import { manageDealerInfo } from './dealer.js'
import {
    createElement,
    activateFormBtn,
    closeErrorBox
} from './primaryFormOperations.js'

manageDealerInfo()
manageProductInfo('purchase')
activateFormBtn()

window.onload = () => {
    createElement('purchase')
    sessionStorage.clear()
}
let productCloseImg = document.getElementById('product-close-img')
let productSelectBtn = document.getElementById('product-select-button')
let totalStock = document.getElementById('totalStock')
let totalTax = document.getElementById('totalTax')
let totalEntries = document.getElementById('stockEntries')
let totalStockAmount = document.getElementById('stockAmt')
let purchaseTable = document.getElementById('stock-table')
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
        let qty = parseInt(quantityEle[i].value)
        let p = quantityEle[i].parentElement.parentElement
        let rate = parseInt(p.children[4].children[0].value)
        let taxRate = parseInt(p.children[7].children[0].value)
        let CGST = p.children[8].children[0]
        let SGST = p.children[9].children[0]
        let rateWithGST = p.children[10].children[0]
        let amt = p.children[11].children[0]

        if (isNaN(qty)) qty = 0
        if (isNaN(rate)) rate = 0
        if (isNaN(taxRate) || taxRate == 0) continue
        else {
            CGST.value = taxRate / 2
            SGST.value = taxRate / 2
        }
        tax = tax + (rate * qty * taxRate) / 100
        rateWithGST.value = (rate + (rate * taxRate) / 100).toFixed(2)
        amt.value = (parseFloat(rateWithGST.value) * qty).toFixed(2)
        mul = mul + parseFloat(amt.value)
    }

    if (tax == 'infinity') totalTax.value = 0
    else totalTax.value = tax.toFixed(2)

    totalStockAmount.value = mul.toFixed(2)
    totalStock.value = sum.toFixed(2)
    totalEntries.value = count
}

function displaySelected (e, d, purchaseEntryRelation = null) {
    let p = e.parentElement.parentElement
    if (purchaseEntryRelation == null) {
        p.children[0].children[0].value = d[0].id
        p.children[1].children[0].value = d[0].product_id
        p.children[2].children[0].value = d[0].product_name
        p.children[3].children[0].value = d[0].quantity
        p.children[4].children[0].value = d[0].rate
        p.children[5].children[0].value = d[0].rate
        p.children[6].children[0].value = d[0].MRP
        p.children[7].children[0].value = d[0].GST
        p.children[8].children[0].value = d[0].GST / 2
        p.children[9].children[0].value = d[0].GST / 2
    } else {
        p.children[0].children[0].value = purchaseEntryRelation.id
        p.children[1].children[0].value = purchaseEntryRelation.product_id
        p.children[2].children[0].value = purchaseEntryRelation.product_name
        p.children[3].children[0].value = purchaseEntryRelation.quantity
        p.children[4].children[0].value = purchaseEntryRelation.purchase_rate
        p.children[5].children[0].value = purchaseEntryRelation.sale_rate
        p.children[6].children[0].value = purchaseEntryRelation.MRP
        p.children[7].children[0].value = purchaseEntryRelation.GST
        p.children[8].children[0].value = purchaseEntryRelation.GST / 2
        p.children[9].children[0].value = purchaseEntryRelation.GST / 2
        p.children[12].children[0].value = purchaseEntryRelation.productMFD
        p.children[13].children[0].value = purchaseEntryRelation.productEXP
    }

    p.children[1].children[0].setAttribute('readonly', true)
}

function manageDynamicRows () {
    let pId = document.querySelectorAll('.productId')
    let searchBar = document.getElementById('search-product')
    pId.forEach(id => {
        id.addEventListener('keyup', e => {
            e.preventDefault()
            if (e.key == 'q' && e.altKey) {
                document
                    .getElementById('product-container')
                    .classList.remove('d-none')
                id.setAttribute('id', 'clickedRow')
                searchBar.focus()
            }
        })
    })

    let productSelectBtn = document.getElementById('product-select-button')
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

    let tableRows = document.querySelectorAll('.stockTableRow')
    tableRows.forEach(ele => {
        ele.addEventListener('keyup', e => {
            e.preventDefault()
            if (
                e.key == 'q' &&
                e.ctrlKey &&
                sessionStorage.getItem('preventForRemoving') == null
            ) {
                ele.remove()
                performEditing()
                let tableRows = document.querySelectorAll('.stockTableRow')
                if (tableRows.length <= 0) createElement('purchase')
            }
        })
    })
    const validatingBeforeCreatingNewRow = (ele, e) => {
        let p = ele.parentElement.parentElement
        if (
            e.key == 'Enter' &&
            sessionStorage.getItem('preventForRemoving') == null
        ) {
            let v = p.children[0].children[0].value
            if (v != null && v != '') {
                let d1 = p.children[12].children[0]
                let d2 = p.children[13].children[0]
                if (
                    d1.value == '' ||
                    d1.value == null ||
                    d2.value == '' ||
                    d2.value == null
                ) {
                    alert('Please fill dates..')
                } else {
                    if (p.nextElementSibling == null) createElement('purchase')
                    else p.nextElementSibling.children[1].children[0].focus()
                }
            } else {
                p.children[1].children[0].focus()
            }
        }
    }
    let gst = document.querySelectorAll('.gst')
    gst.forEach(ele => {
        ele.addEventListener('keyup', e => {
            e.preventDefault()
            validatingBeforeCreatingNewRow(ele, e)
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
    editableFields.forEach(ele => {
        ele.addEventListener('mousemove', e => {
            e.preventDefault()
            performEditing()
        })

        ele.addEventListener('keyup', e => {
            e.preventDefault()
            if (e.key == 'Enter') {
                performEditing()
            }
        })
    })

    let productId = document.querySelectorAll('.productId')
    productId.forEach(ele => {
        ele.addEventListener('keyup', e => {
            e.preventDefault()
            if (e.key == 'Enter') {
                fetch(`product/${ele.value.trim()}`)
                    .then(res => res.json())
                    .then(data => {
                        if ('error' in data[0]) {
                            alert(data[0].error)
                        } else {
                            displaySelected(ele, data)
                            ele.parentElement.nextElementSibling.nextElementSibling.children[0].focus()
                        }
                    })
            }
        })
    })
}
manageDynamicRows()
let observe = new MutationObserver(() => {
    manageDynamicRows()
})
observe.observe(purchaseTable, { childList: true })

let entry = document.getElementById('entryNumber')
let date = document.getElementById('date')
let purchaseForm = document.getElementById('purchaseEntryForm')
let purchaseFormEle = document.querySelectorAll('#purchaseEntryForm div input')
entry.addEventListener('keyup', e => {
    e.preventDefault()
    if (e.key == 'Enter') {
        fetch(`purchase/${entry.value.trim()}/${date.value.trim()}`)
            .then(res => res.json())
            .then(data => {
                if ('error' in data[0]) {
                    alert(data[0].error)
                } else {
                    if (data[0].dealer != null) {
                        purchaseFormEle[0].value = data[0].dealer.id
                        purchaseFormEle[1].value = data[0].dealer.dealer_name
                        purchaseFormEle[2].value = data[0].dealer.contact
                        purchaseFormEle[3].value = data[0].dealer.email
                        purchaseFormEle[4].value = data[0].dealer.GST_no
                    }
                    let tableRows = document.querySelectorAll('.stockTableRow')
                    tableRows.forEach(row => {
                        row.remove()
                    })
                    data[0].product.forEach(ele => {
                        createElement('purchase')
                        let e = document.getElementById('parent')
                        let obj = {
                            id: ele.id,
                            product_id: ele.product_id,
                            product_name: ele.product_name,
                            quantity: ele.pivot.addedQuantity,
                            purchase_rate: ele.pivot.purchase_rate,
                            sale_rate: ele.pivot.sale_rate,
                            MRP: ele.pivot.MRP,
                            GST: ele.pivot.GST,
                            productMFD: ele.pivot.productMFD,
                            productEXP: ele.pivot.productEXP
                        }
                        displaySelected(e, data, obj)
                        e.removeAttribute('id')
                    })
                    performEditing()
                    sessionStorage.setItem(
                        'preventForRemoving',
                        'updateORdelete'
                    )

                    purchaseForm.setAttribute(
                        'action',
                        `http://localhost:8000/purchase/${entry.value.trim()}/${date.value.trim()}`
                    )
                    let method = document.getElementById('method')
                    let input = document.createElement('input')
                    input.setAttribute('name', '_method')
                    input.setAttribute('type', 'hidden')
                    if (sessionStorage.getItem('update') != null)
                        input.value = 'PUT'
                    else input.value = 'DELETE'

                    method.append(input)
                }
            })
    }
})

closeErrorBox()
document.addEventListener('contextmenu', e => {
    e.preventDefault()
})
