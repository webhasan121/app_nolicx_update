import { Children, isValidElement } from "react";
import useTranslation from "../../../hooks/useTranslation";

const countColumns = (children) => {
  let count = 0;

  Children.forEach(children, (child) => {
    if (!isValidElement(child)) {
      return;
    }

    if (child.type === "th") {
      count += 1;
      return;
    }

    count += countColumns(child.props.children);
  });

  return count;
};

export default function Table({
  data = [],
  children,
  emptyMessage = "Data Not Found",
  tableClassName = "",
  ...props
}) {
  const { t } = useTranslation();
  const hasData = Array.isArray(data) ? data.length > 0 : Boolean(data?.length);
  const columnCount = Math.max(countColumns(children), 1);
  const translatedEmptyMessage =
    typeof emptyMessage === "string" ? t(emptyMessage) : emptyMessage;

  return (
    <div {...props} className={`overflow-hidden overflow-x-auto ${props.className ?? ""}`}>

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

      <table
        id="myTable"
        className={`mb-2 w-full border-collapse border ${tableClassName}`}
      >
        {children}
        {!hasData && (
          <tbody>
            <tr>
              <td
                colSpan={columnCount}
                className="py-8 font-medium text-center text-slate-500"
              >
                {translatedEmptyMessage}
              </td>
            </tr>
          </tbody>
        )}
      </table>
    </div>
  );
}
