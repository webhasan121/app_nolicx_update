import { Children, isValidElement, cloneElement } from "react";
import useTranslation from "../hooks/useTranslation";

export default function InputLabel({
  htmlFor,
  children,
  className = "",
  ...props
}) {
  const { t } = useTranslation();
  const translateChild = (child) => {
    if (typeof child === "string") {
      return child.trim() ? t(child) : child;
    }

    if (Array.isArray(child)) {
      return child.map(translateChild);
    }

    if (isValidElement(child) && child.props?.children) {
      return cloneElement(child, {
        children: Children.map(child.props.children, translateChild),
      });
    }

    return child;
  };

  return (
    <label
      htmlFor={htmlFor}
      className={`block font-medium text-sm text-gray-700 ${className}`}
      {...props}
    >
      {Children.map(children, translateChild)}
    </label>
  );
}
