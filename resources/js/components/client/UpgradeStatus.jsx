export default function UpgradeStatus({ authRequest }) {
    if (!authRequest) return null;

    return (
        <div>
            {authRequest.status === "Pending" && (
                <div
                    className="text-sm py-1 border-b border-t p-2 rounded"
                    style={{ backgroundColor: "#fefcbf", color: "#b45309" }}
                >
                    <strong>Pending</strong>, Your account is under reveiw now. stay with patience.
                </div>
            )}

            {authRequest.status === "Active" && (
                <div
                    className="text-sm py-1 border-b border-t p-2 rounded"
                    style={{ backgroundColor: "#bbf7d0", color: "#166534" }}
                >
                    Your Membership is now in <strong>{authRequest.status}</strong> with
                    <strong>{authRequest.system_get_comission ?? "0"}%</strong> comission sharing . Now you can
                    sell your products.
                </div>
            )}

            {(authRequest.status === "Disabled" || authRequest.status === "Suspended") && (
                <div
                    className="text-sm py-1 border-b border-t p-2 rounded"
                    style={{ backgroundColor: "#fee2e2", color: "#b91c1c" }}
                >
                    Your Membership is now <strong>{authRequest.status}</strong> .{" "}
                    <strong>{authRequest.rejected_for ?? "For unknown reason "}</strong>
                </div>
            )}

            {authRequest.documents?.deatline && (
                <div className="py-3 text-xs">
                    You are requested to fill your required document, with deatline of{" "}
                    <strong>{new Date(authRequest.documents.deatline).toDateString()} *.</strong> After successfully
                    authorize your document, you will be able to do your vendor daily jobs. Otherwise, you will be
                    suspended.
                </div>
            )}
        </div>
    );
}
