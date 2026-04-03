export default function OrderStatus({ status }) {
  const statuses = {
    Pending: {
      text: "Pending",
      className: "px-2 py-1 bg-gray-300 rounded-lg",
    },
    Picked: {
      text: "Picked",
      className: "px-2 py-1 text-white rounded-lg bg-sky-300",
    },
    Delivery: {
      text: "Delivery",
      className: "px-2 py-1 text-white rounded-lg bg-sky-300",
    },
    Delivered: {
      text: "Delivered",
      className: "px-2 py-1 text-white bg-indigo-300 rounded-lg",
    },
    Confirm: {
      text: "Finished",
      className: "px-2 py-1 text-white bg-green-900 rounded-lg",
    },
    Accept: {
      text: "Accept",
      className: "px-2 py-1 text-white bg-indigo-900 rounded-lg",
    },
    Cancel: {
      text: "Reject",
      className: "px-2 py-1 bg-red-300 rounded-lg",
    },
    Hold: {
      text: "Hold",
      className: "px-2 py-1 bg-red-300 rounded-lg",
    },
    Cancelled: {
      text: "Buyer Cancelled",
      className: "px-2 py-1 text-white bg-gray-300 rounded-lg",
    },
  };

  if (!statuses[status]) return null;

  return (
    <span className={statuses[status].className}>
      {statuses[status].text}
    </span>
  );
}
