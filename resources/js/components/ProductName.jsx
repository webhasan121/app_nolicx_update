export default function ProductName({ children, value, limit = 24, className = "" }) {
    const text = String(value ?? children ?? "N/A");
    const display = text.length > limit ? `${text.slice(0, limit).trimEnd()}...` : text;

    return (
        <span title={text} className={`inline-block max-w-48 align-top ${className}`}>
            {display}
        </span>
    );
}
