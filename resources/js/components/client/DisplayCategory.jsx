import { useRef } from "react";
import NavLink from "../NavLink";
import useTranslation from "../../hooks/useTranslation";

export default function DisplayCategory({ categories = [] }) {
  const catDivRef = useRef(null);
  const catWrapRef = useRef(null);
  const { t } = useTranslation();



  if (!categories.length) return null;

  return (
    <div>
      <div
        className="relative py-4 overflow-x-scroll"
        id="cat_div"
        ref={catDivRef}
      >
        <div
          id="cat_wrapper"
          className="flex gap-3"
          ref={catWrapRef}
        >
          {categories
            .filter((item) => item.slug !== "default-category")
            .map((item) => {
              const label = t(item.name ?? "");

              return (
              <div
                key={item.id}
                className="text-center bg-white rounded-md cat_item"
                style={{
                  backdropFilter: "blur(3px)",
                  width: "100px",
                  height: "100px",
                }}
              >
                <NavLink
                  href={route("category.products", { cat: item.slug })}
                  className="flex flex-col items-center w-full h-full p-0 border-b-0 text-inherit hover:text-inherit hover:border-transparent"
                  style={{
                    width: "100px",
                    height: "100px",
                  }}
                >
                  <img
                    src={`/storage/${item.image}`}
                    alt={label}
                    className="rounded-md"
                    style={{
                      width: "100px",
                      height: "100px",
                    }}
                  />

                  <div
                    className="absolute bottom-0 w-full pt-1 text-center"
                    style={{
                      backgroundColor: "#f6f6f69c",
                      backdropFilter: "blur(6px)",
                    }}
                  >
                    {label.length > 9
                      ? label.substring(0, 9) + "..."
                      : label}
                  </div>
                </NavLink>
              </div>
            )})}
        </div>

      </div>
    </div>
  );
}
