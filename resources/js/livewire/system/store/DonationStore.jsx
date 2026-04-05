export default function DonationStore({ store = 0 }) {
    return (
        <div className="">
            <div className="rounded bg-white text-center">
                <div className="border border-green-900 rounded md:flex justify-between items-center p-2">
                    <div className="px-3 lg:p-3 bold text-start flex justify-between items-center md:block">
                        <div className="fs-5 fw-bold text-start ">
                            <a href="" className="flex items-center">
                                <i className="fas fa-store fs-6 pe-2"></i>
                                Domain
                            </a>
                        </div>
                        <div className="hidden flex items-center text-xs">
                            <div className="text-start text-red-900">
                                <i className="fas fa-long-arrow-alt-up"></i>
                            </div>
                            <div className="px-3">|</div>
                            <div className="text-green-900">
                                <i className="fas fa-long-arrow-alt-down"></i>
                            </div>
                        </div>
                    </div>
                    <div className="px-3 py-1 lg:p-3 text-lg fw-bold text-green-900">
                        {store}
                    </div>
                </div>
            </div>
        </div>
    );
}
