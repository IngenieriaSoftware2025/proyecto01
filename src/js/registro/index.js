
import DataTable from "datatables.net-bs5";
import { removeBootstrapValidation, soloNumeros, Toast } from "../funciones";
import { Modal, Dropdown } from "bootstrap";
import Swal from "sweetalert2";

const formUsuario = document.getElementById('formUsuario')

const guardarUsuario = async e => {
  e.preventDefault();
  
  try {

    const body = new FormData(formUsuario)
    const url = "/proyecto01/usuarios/guardar"
    const config = {
      method: 'POST',
      body
    }

    const respuesta = await fetch(url, config);
    const data = await respuesta.json();
    const { codigo, mensaje, detalle } = data;
        console.log(data)
    let icon = 'info'
    if (codigo == 1) {
      icon = 'success'
      formUsuario.reset()
     

    } else if (codigo == 2) {
      icon = 'warning'

      console.log(detalle);
    } else if (codigo == 0) {
      icon = 'error'
      console.log(detalle);

    }

    Toast.fire({
      icon,
      title: mensaje
    })

  } catch (error) {
    console.log(error);
  }

}

formUsuario.addEventListener('submit', guardarUsuario)

