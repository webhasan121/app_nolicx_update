export default function Section({ children }) {
    return (
        <div
            style={{
                display: "grid",
                gridTemplateColumns: "repeat(auto-fill, minmax(150px, 1fr))",
                gridGap: "20px",
            }}
        >
            {children}
        </div>
    );
}
