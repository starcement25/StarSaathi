const formatINR = (value) => {
    const num = Number(value).toFixed(2);
    const [integer, decimal] = num.split('.');
    const lastThree = integer.slice(-3);
    const rest = integer.slice(0, -3);
    const formatted = rest
      ? rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + lastThree
      : lastThree;
    return formatted + '.' + decimal;
  };

  export default formatINR;