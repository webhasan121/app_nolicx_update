export default function Image({ src, className = "", style, alt = "IMAGE", ...props }) {
  return (
    <div>
      <img
        src={src}
        style={{ width: "150px", height: "100px", ...(style || {}) }}
        className={className}
        alt={alt}
        {...props}
      />
    </div>
  );
}

