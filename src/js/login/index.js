console.log("Hola Mundo 3");
import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

const FormUsuario = document.getElementById("FormUsuario");
const BtnGuardar = document.getElementById("BtnGuardar");

const guardarAPI = async (e) => {
  e.preventDefault();
  BtnGuardar.disabled = true;

  if (!validarFormulario(FormUsuario, ["usuario_id"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campo obligatorio",
      text: "todos los campos son obligatotios",
      showConfirmButton: true,
    });
    BtnGuardar.disabled = false;
    return;
  }

  const body = new FormData(FormUsuario);
  const url = "/proyecto01/guardarAPI";
  const config = {
    method: "POST",
    body,
  };

  try {
    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();
    console.log(datos);
    const { codigo, mensaje } = datos;

    if (codigo === 1) {
      await Swal.fire({
        position: "center",
        icon: "success",
        title: "Exito",
        text: mensaje,
        showConfirmButton: true,
      });
      limpiarTodo();
      buscarAPI();
    }
  } catch (error) {
    console.log(error);
  }
  BtnGuardar.disabled = false;
};


FormUsuario.addEventListener("submit", guardarAPI)