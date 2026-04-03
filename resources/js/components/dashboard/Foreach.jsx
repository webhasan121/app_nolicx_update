export default function Foreach({ data, children }) {
    return (
        <div>
            {Array.isArray(data) && data.length > 0 ? (
                children
            ) : (
                <div className="alert alert-danger">No Data Found !</div>
            )}
        </div>
    );
}
