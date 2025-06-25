import "./bootstrap"
import Alpine from "alpinejs"
import axios from "axios"

window.Alpine = Alpine
window.axios = axios

// Configurar axios
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest"

// Token CSRF
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
  axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content
}

// Funciones globales
window.formatCurrency = (amount) =>
  new Intl.NumberFormat("es-PE", {
    style: "currency",
    currency: "PEN",
  }).format(amount)

window.formatDate = (date) => new Intl.DateTimeFormat("es-PE").format(new Date(date))

window.confirmDelete = (message) => confirm(message || "¿Estás seguro de eliminar este elemento?")

Alpine.start()
