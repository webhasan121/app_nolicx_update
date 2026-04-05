export default function CoinStore({ store = 0, take = 0, give = 0 }) {
    return (
        <div>
            <div className=" rounded w-full bg-white text-center">
                <div className="border border-green-900 rounded md:flex justify-between items-start p-2">
                    <div className="px-3 py-1 lg:p-3 bold text-center flex justify-between items-center md:block">
                        <div className="fs-5 fw-bold text-start w-full">
                            <a href="" className="flex items-center">
                                <i className="fas fa-store fs-6 p-2"></i>
                                Comission Store
                            </a>
                        </div>
                    </div>
                    <div className="px-3 text-center py-1 lg:p-3  text-lg fw-bold text-green-900">
                        <div className="font-bold px-2 border rounded">
                            {store}
                        </div>

                        <div className=" py-2">
                            <div className="flex justify-center items-center text-xs">
                                <div className="text-start text-red-900" style={{ color: "red" }}>
                                    {give}
                                    <i className="fas fa-long-arrow-alt-up"></i>
                                </div>
                                <div className="px-3">|</div>
                                <div className="text-green-900" style={{ color: "green" }}>
                                    <i className="fas fa-long-arrow-alt-down"></i>
                                    {take}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="px-3 py-1 lg:p-3 text-end">
                        <div className="flex items-center text-xs">
                            <div className="text-start text-red-900" style={{ color: "red" }}>
                                <i className="fas fa-long-arrow-alt-up"></i>
                            </div>
                            <div className="px-3">|</div>
                            <div className="text-green-900" style={{ color: "green" }}>
                                <i className="fas fa-long-arrow-alt-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
