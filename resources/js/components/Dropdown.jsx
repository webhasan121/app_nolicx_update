import { useState, useRef, useEffect } from "react";
import clsx from "clsx";

export default function Dropdown({
  align = "right",
  width = "48",
  contentClasses = "py-1 bg-white",
  trigger,
  children,
}) {
  const [open, setOpen] = useState(false);
  const ref = useRef(null);

  // Close on outside click
  useEffect(() => {
    const handler = (e) => {
      if (ref.current && !ref.current.contains(e.target)) {
        setOpen(false);
      }
    };
    document.addEventListener("mousedown", handler);
    return () => document.removeEventListener("mousedown", handler);
  }, []);

  const alignment =
    align === "left"
      ? "origin-top-left left-0"
      : "origin-top-right right-0";

  const widthClass = width === "48" ? "w-48" : width;

  return (
    <div className="relative" ref={ref}>
      <div onClick={() => setOpen(!open)}>
        {trigger}
      </div>

      {open && (
        <div
          className={clsx(
            "absolute z-50 mt-2 rounded-md shadow-lg transition transform duration-200",
            alignment,
            widthClass
          )}
        >
          <div
            className={clsx(
              "rounded-md ring-1 ring-black ring-opacity-5",
              contentClasses
            )}
            onClick={() => setOpen(false)}
          >
            {children}
          </div>
        </div>
      )}
    </div>
  );
}
