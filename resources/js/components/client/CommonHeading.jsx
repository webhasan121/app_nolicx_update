import ApplicationName from "../ApplicationName";

export default function CommonHeading() {
    return (
        <div>
            <div className="w-full w-auto mb-3 text-3xl text-center heading_center">
                <h2 className="flex justify-center gap-3">
                    <ApplicationName />
                    <span className="font-bold text-green-900">Marketplace</span>
                </h2>
            </div>
        </div>
    );
}
