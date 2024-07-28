import Swal from "sweetalert2";

export function showAlert(title, text, type) {
    return Swal.fire({
        title,
        text,
        icon: type,
        confirmButtonColor: "#3085d6",
    });
}

export function showAlertConfirmation(title, text, type) {
    const defaultOptions = {
        title,
        text,
        icon: type,
        showCancelButton: true,
        confirmButtonText: "Sí",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
    };
    return Swal.fire({ ...defaultOptions });
}
