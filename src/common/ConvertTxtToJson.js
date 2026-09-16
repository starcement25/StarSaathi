export const convertTxtToJson = (text) => {
    const rawText = text
    const lines = rawText.trim().split('\n')
    const [tripId, routeId] = lines[0].split('¥')
    const [date, time] = lines[1].split('€')
    var stations = {}
    if (tripId != 0 && routeId != 0) {
        stations = lines.slice(2).map(line => {
            const [code, name, type] = line.split('^')
            return { code, name, type }
        })
    }
    return { tripId, routeId, date, time, stations }
}