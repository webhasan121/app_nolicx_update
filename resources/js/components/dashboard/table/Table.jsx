export default function Table({ data = [], children, ...props }) {
  return (
    <div {...props} className={`overflow-hidden overflow-x-scroll ${props.className ?? ""}`}>

      <style>
        {`
          thead th {
            border-bottom: 2px solid #dee2e6;
            padding: 12px;
            font-size: 15px;
            text-align: left;
          }

          td {
            padding: 12px;
            font-size: 14px;
          }
        `}
      </style>

      {data && data.length > 0 ? (
        <table id="myTable" className="w-full mb-2 border border-collapse">
          {children}
        </table>
      ) : (
        <div className="alert alert-danger">
          No Data Found !
        </div>
      )}
    </div>
  );
}
