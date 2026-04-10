function hasItems(data) {
    if (data === null || data === undefined) {
        return false;
    }

    if (Array.isArray(data)) {
        return data.length > 0;
    }

    if (typeof data === "string") {
        return data.length > 0;
    }

    if (typeof data === "object") {
        if (typeof data.length === "number") {
            return data.length > 0;
        }

        if (Array.isArray(data.data)) {
            return data.data.length > 0;
        }

        if (typeof data.total === "number") {
            return data.total > 0;
        }

        return Object.keys(data).length > 0;
    }

    return false;
}

export default function Foreach({ data, children }) {
    return <div>{hasItems(data) ? children : <div className="alert alert-danger">No Data Found !</div>}</div>;
}
