import { useToast } from "vue-toastification";
import api from "./api";
const toast = useToast();

export const payWithChapa = async (payroll) => {
  try {
    const res = await api.post(`/payrolls/${payroll.id}/chapa/pay`);

    // Backend will initialize the Chapa transaction and respond with checkout_url
    if (res.data.checkout_url) {
      window.location.href = res.data.checkout_url; // redirect user to Chapa payment page
    }
  } catch (error) {
    console.error("Chapa payment init failed:", error);
    toast.error("❌ Failed to initialize Chapa payment");
  }
};
