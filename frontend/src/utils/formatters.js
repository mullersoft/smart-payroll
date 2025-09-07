// D:\qelem meda\smart-payroll\frontend\src\utils\formatters.js
export const formatCurrency = (value) => {
  const num = parseFloat(value || 0);
  return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

export const formatDate = (dateString, format = 'full') => {
  if (!dateString) return 'N/A';

  const date = new Date(dateString);
  if (format === 'monthYear') {
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
  }

  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};
