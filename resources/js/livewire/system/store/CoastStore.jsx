export default function CoastStore({ store = 0 }) {
    return (
        <div>
            <div className="rounded bg-white text-center">
                <div className="border border-green-900 rounded md:flex justify-between items-center p-2">
                    <div className="px-3 py-1 p-lg-3 bold text-start flex justify-between items-center md:block">
                        <div className="fs-5 fw-bold text-start ">
                            <a href="" className="flex items-center">
                                <i className="fas fa-store text-md pe-2"></i>
                                Server Cost
                            </a>
                        </div>
                        <div className="hidden flex items-center text-xs">
                            <div className="text-start text-danger" style={{ color: "red" }}>
                                <i className="fas fa-long-arrow-alt-up"></i>
                            </div>
                            <div className="px-3">|</div>
                            <div style={{ color: "green" }}>
                                <i className="fas fa-long-arrow-alt-down"></i>
                            </div>
                        </div>
                    </div>
                    <div className="px-3 py-1 lg:p-3 text-lg fw-bold " style={{ color: "green" }}>
                        {store}
                    </div>
                </div>
            </div>
        </div>
    );
}
